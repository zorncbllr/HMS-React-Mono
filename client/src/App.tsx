import { Route, Routes } from "react-router-dom";
import Home from "./pages/Home";
import { AppSidebar } from "./components/app-sidebar";
import { SidebarProvider, SidebarTrigger } from "./components/ui/sidebar";
import Settings from "./pages/Settings";

function App() {
  return (
    <SidebarProvider>
      <AppSidebar />
      <main>
        <SidebarTrigger />

        <Routes>
          <Route path="/" Component={Home} />
          <Route path="/settings" Component={Settings} />
        </Routes>
      </main>
    </SidebarProvider>
  );
}

export default App;
