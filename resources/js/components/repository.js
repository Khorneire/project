import { extractData, useRequest } from "../use-request";

export const repository = () => {
  const request = useRequest("/api");

  const getData = () => request.get("persons").then(extractData());

  return {
    getData,
  };
};
