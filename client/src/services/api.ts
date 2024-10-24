import axios from "axios";
import { Msg } from "../interfaces/msg";

const axiosInstance = axios.create({
  baseURL: "http://localhost:3000",
});

export const getMsg = async () => {
  return (await axiosInstance.get<Msg>("/")).data;
};
