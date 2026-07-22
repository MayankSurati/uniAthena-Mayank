import { Menu, Bell, Search } from "lucide-react";
import { useSidebar } from "../../contexts/SidebarContext";

export default function Header() {
    const { toggleSidebar } = useSidebar();

    return (
        <header className="h-16 bg-white border-b flex items-center justify-between px-6 shadow-sm">
            <button onClick={toggleSidebar}>
                <Menu />
            </button>

            <div className="flex items-center gap-2 border rounded-lg px-3 py-2 w-80">
                <Search size={18} />
                <input
                    className="outline-none w-full"
                    placeholder="Search..."
                />
            </div>

            <div className="flex items-center gap-6">
                <Bell />
                <img
                    src="https://i.pravatar.cc/40"
                    alt="User Avatar"
                    className="rounded-full w-10 h-10"
                />
            </div>
        </header>
    );
}