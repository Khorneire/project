<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\MockObject\MockObject;
use Throwable;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use EntityManagerAwareTrait;
    use DatabaseInteractionsTrait;
    use DatabaseTransactionsTrait;

    public const NOT_NULL = 'not null';

    /**
     * @var TestResponse|null
     */
    protected ?TestResponse $previousResponse = null;

    /**
     * @param string $method
     * @param string $uri
     * @param array $parameters
     * @param array $cookies
     * @param array $files
     * @param array $server
     * @param null $content
     * @return TestResponse|null
     */
    public function call($method, $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null)
    {
        $this->previousResponse = null;
        $this->previousResponse = parent::call($method, $uri, $parameters, $cookies, $files, $server, $content);

        // As we're using sub-requests, clear the Unit of Work within Doctrine so there aren't any
        // uncommitted changes leaking between calls.
        foreach ($this->getDoctrineManager()->all() as $entityManager) {
            $entityManager->getUnitOfWork()->clear();
        }

        return $this->previousResponse;
    }

    /**
     * @return array
     */
    protected function getJsonResponse(): array
    {
        return $this->previousResponse->json();
    }

    /**
     * Asserts pagination details are returned in an API response.
     *
     * @param int $expectedNumberOfResults
     * @param int $expectedTotalPossibleResults
     * @param int|null $expectedCurrentPage
     * @param int|null $expectedTotalNumberOfPages
     *
     * @return $this
     */
    protected function seePagination(
        int $expectedNumberOfResults,
        int $expectedTotalPossibleResults,
        ?int $expectedCurrentPage = 1,
        ?int $expectedTotalNumberOfPages = null
    ): self  {

        $this->assertTrue(
            !empty($this->previousResponse->json()['pagination']),
            'Expected to see a pagination details, none found.'
        );

        $this->assertTrue(
            count($this->previousResponse->json()['data']) === $expectedNumberOfResults,
            sprintf(
                'Expected to see %d current results, %d returned.',
                $expectedNumberOfResults,
                count($this->previousResponse->json()['data'])
            )
        );

        $this->assertTrue(
            $this->previousResponse->json()['pagination']['counts']['total'] === $expectedTotalPossibleResults,
            sprintf(
                'Expected to see %d total possible results, %d returned.',
                $expectedTotalPossibleResults,
                $this->previousResponse->json()['pagination']['counts']['total']
            )
        );

        $this->assertTrue(
            $this->previousResponse->json()['pagination']['pages']['current'] === $expectedCurrentPage,
            sprintf(
                'Expected current page to be %d, %d returned.',
                $expectedCurrentPage,
                $this->previousResponse->json()['pagination']['pages']['current']
            )
        );

        $this->assertTrue(
            $this->previousResponse->json()['pagination']['pages']['total'] === $expectedTotalNumberOfPages,
            sprintf(
                'Expected total number of possible pages to be %d, %d returned.',
                $expectedTotalNumberOfPages,
                $this->previousResponse->json()['pagination']['pages']['total']
            )
        );

        return $this;
    }

    /**
     * Asserts the previous call didn't include an error response.
     *
     * @return self
     */
    protected function dontSeeError(): self
    {
        $this->assertTrue(
            $this->previousResponse->getStatusCode() === 204 || !isset($this->previousResponse->json()['error']),
            'Expected to see a successful response, error response found.'
        );

        return $this;
    }

    /**
     * Asserts the previous call included an error response.
     *
     * @param string|null $message
     * @return self
     */
    protected function seeError(?string $message = null): self
    {
        $response = $this->previousResponse->json();

        $this->assertTrue(!empty($response['error']));

        if ($message !== null) {
            $this->assertTrue(($response['error']['message'] ?? null) === $message);
        }

        return $this;
    }

    /**
     * Asserts the application error code was returned.
     *
     * @param int $code
     * @return self
     */
    protected function seeApplicationErrorCode(int $code): self
    {
        $this->assertSame($this->previousResponse->json()['error']['application'] ?? '', $code);

        return $this;
    }

    /**
     * Asserts the given list of fields are returned in the validation error response.
     *
     * @param array $fields
     * @return $this
     */
    protected function seeValidationErrorsForFields(array $fields): self
    {
        $response = $this->previousResponse->json();

        foreach ($fields as $field) {
            $this->assertTrue(
                (!empty($response['error']['fields'][$field])),
                sprintf('Failed asserting the field \'%s\' is present within the validation response.', $field)
            );
        }

        return $this;
    }

    /**
     * This method will return a mock version of the given class for the given methods.
     * This will then be registered into the container.
     *
     * Note this is a helper method and it is suitable for the majority of mocking scenarios.
     * If you require a different configuration you should use MockBuilder directly in your tests.
     *
     * @param string $class
     * @param array $methods
     * @return MockObject
     */
    protected function getMock(string $class, array $methods): MockObject
    {
        $mock = $this->getMockBuilder($class)
            ->disableOriginalConstructor()
            ->onlyMethods($methods)
            ->getMock();

        $this->app->instance($class, $mock);

        return $mock;
    }

    /**
     * Helper method to test Jobs and also perform an assertion on the failed Jobs handler.
     * Bubbled up exceptions to be handled by PHPUnit's usual Exception Handling process.
     *
     * @param $job
     * @param callable|null $failureCallback
     *
     * @throws Throwable
     */
    protected function testJob($job, ?callable $failureCallback = null)
    {
        try {
            dispatch_sync($job);
        } catch (Throwable $exception) {
            if (method_exists($job, 'failed')) {
                $job->failed($exception);
            }
            if ($failureCallback !== null) {
                $failureCallback();
            }
            throw $exception;
        }
    }
}
