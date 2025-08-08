import axios from "axios";

let _nextHeaders;
let _globalHeaders;

export const useRequest = (baseUrl = "/api", params = {}) => {
  let headers = params?.headers || {};
  headers = {
    ..._globalHeaders,
    ...headers,
  };

  const get = (endpoint, headers = {}) => {
    return makeRequest("GET", getUrl(endpoint), headers);
  };

  const post = (endpoint, payload = {}, headers = {}) => {
    if (typeof endpoint === "object") {
      payload = endpoint;
      endpoint = "";
    }
    return makeRequest("POST", getUrl(endpoint), payload, headers);
  };

  const patch = (endpoint, payload = {}, headers = {}) => {
    if (typeof endpoint === "object") {
      payload = endpoint;
      endpoint = "";
    }
    return makeRequest("PATCH", getUrl(endpoint), payload, headers);
  };

  const destroy = (endpoint, payload = {}, headers = {}) => {
    if (typeof endpoint === "object") {
      payload = endpoint;
      endpoint = "";
    }
    return makeRequest("DELETE", getUrl(endpoint), payload, headers);
  };

  const getUrl = (string) => {
    let endpoint = `${baseUrl}/${string}`;
    endpoint = endpoint.endsWith("/") ? endpoint.slice(0, -1) : endpoint;
    return endpoint.replaceAll("/?", "?");
  };

  const makeRequest = (method, url, data, passedHeaders) => {
    const nextRequestHeaders = _nextHeaders || {};
    const requestHeaders = {
      ...headers,
      ...passedHeaders,
      ..._globalHeaders,
      ...nextRequestHeaders,
    };

    const request = axios
      .request({
        method,
        url,
        data,
        headers: requestHeaders,
        responseType: "json",
      })
      .catch((error) => {
        return Promise.reject({
          status: error.response.status,
          message:
            error.response.data.error?.message || "Unknown error occurred",
        });
      });
    _nextHeaders = undefined;
    return request;
  };

  return { get, post, patch, destroy };
};

export const extractData = (nestedData = false) => {
  if (nestedData) {
    return ({ data }) => data.data;
  }
  return ({ data }) => data;
};

export const setGlobalRequestHeaders = (globalHeaders) =>
  (_globalHeaders = globalHeaders);
export const setNextRequestHeaders = (newHeaders) =>
  (_nextHeaders = newHeaders);
