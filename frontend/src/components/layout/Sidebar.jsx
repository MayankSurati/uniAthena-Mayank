import { NavLink, useNavigate } from "react-router-dom";
import { LogOut } from "lucide-react";

import { sidebarItems } from "../../constants/sidebar";
import { useSidebar } from "../../contexts/SidebarContext";
import { useAuth } from "../../contexts/AuthContext";

export default function Sidebar() {
    const { collapsed } = useSidebar();
    const { logout, user } = useAuth();
    const navigate = useNavigate();

    const handleLogout = async () => {
        await logout();
        navigate("/login", { replace: true });
    };    
    return (
        <aside
            className={`bg-slate-900 text-white min-h-screen transition-all duration-300 ${
                collapsed ? "w-20" : "w-64"
            }`}
        >
            <div className="p-5 text-xl font-bold border-b border-slate-700">
                {collapsed ? "A" : "Admin Panel"}
            </div>

            <nav className="mt-5">
                {sidebarItems.map((item) => {
                    const Icon = item.icon;

                    return (
                        <NavLink
                            key={item.path}
                            to={item.path}
                            className={({ isActive }) =>
                                `flex items-center gap-3 px-5 py-3 transition-colors ${
                                    isActive
                                        ? "bg-blue-600"
                                        : "hover:bg-slate-800"
                                }`
                            }
                        >
                            <Icon size={20} />
                            {!collapsed && <span>{item.title}</span>}
                        </NavLink>
                    );
                })}
            </nav>
            {/* Footer */}
            <div className="border-t border-slate-700 p-4">
                <div className="mb-4">
                    <p className="font-semibold">
                        {user?.name}
                    </p>
                    <p className="text-xs text-gray-400">
                        {user?.email}
                    </p>
                </div>

                <button
                    onClick={handleLogout}
                    className="flex items-center gap-3 w-full rounded-lg bg-red-600 px-4 py-3 hover:bg-red-700 transition"
                >
                    <LogOut size={18} />
                    Logout
                </button>
            </div>
        </aside>
    );
}