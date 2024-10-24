import { useState } from "react";
import reactLogo from "./../assets/react.svg";
import viteLogo from "/vite.svg";
import { useMsg } from "../services/queries";

function Home() {
  const [count, setCount] = useState(0);
  const { data } = useMsg();

  return (
    <div className="h-full flex flex-col gap-6 justify-center items-center">
      <div className="flex gap-4">
        <a href="https://vite.dev" target="_blank">
          <img src={viteLogo} className="w-32" alt="Vite logo" />
        </a>
        <a href="https://react.dev" target="_blank">
          <img
            src={reactLogo}
            className="w-32 animate-pulse"
            alt="React logo"
          />
        </a>
      </div>
      <h1 className="text-5xl font-semibold">Mono + React</h1>
      <div className="flex gap-12 flex-col items-center">
        <button
          className="px-8 w-fit font-semibold py-2 rounded-full text-white bg-blue-600"
          onClick={() => setCount((count) => count + 1)}
        >
          count is {count}
        </button>
        <p className="text-gray-600 text-l">{data?.msg}</p>
      </div>
    </div>
  );
}

export default Home;
