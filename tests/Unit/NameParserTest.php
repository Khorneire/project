<?php

namespace Tests\Unit;

use App\Http\Services\NameParser;

use PHPUnit\Framework\TestCase;

class NameParserTest extends TestCase
{
    private NameParser $parser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parser = new NameParser();
    }

    public function test_parse_simple_name()
    {
        $this->assertEquals([
            'title' => 'Mr',
            'first_name' => 'John',
            'initial' => null,
            'last_name' => 'Smith',
        ], $this->parser->parseName('Mr John Smith'));
    }

    public function test_parse_name_with_initial()
    {
        $this->assertEquals([
            'title' => 'Mr',
            'first_name' => null,
            'initial' => 'F',
            'last_name' => 'Fredrickson',
        ], $this->parser->parseName('Mr F. Fredrickson'));
    }

    public function test_parse_compound_last_name()
    {
        $this->assertEquals([
            'title' => 'Mrs',
            'first_name' => 'Faye',
            'initial' => null,
            'last_name' => 'Hughes-Eastwood',
        ], $this->parser->parseName('Mrs Faye Hughes-Eastwood'));
    }

    public function test_split_names_with_and()
    {
        $result = $this->parser->splitNames('Mr and Mrs Smith');
        $this->assertEquals(['Mr Smith', 'Mrs Smith'], $result);
    }

    public function test_split_multiple_names()
    {
        $result = $this->parser->splitNames('Mr Tom Staff and Mr John Doe');
        $this->assertEquals(['Mr Tom Staff', 'Mr John Doe'], $result);
    }
}
