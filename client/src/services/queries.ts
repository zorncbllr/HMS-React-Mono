import { useQuery } from "@tanstack/react-query";
import { getMsg } from "./api";

export const useMsg = () => {
  return useQuery({
    queryKey: ["msg"],
    queryFn: getMsg,
  });
};
