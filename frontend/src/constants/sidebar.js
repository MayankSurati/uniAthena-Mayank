import {
    LayoutDashboard,
    Package,
    FolderTree,
    LogOut,
} from "lucide-react";

export const sidebarItems = [
    {
        title: "Dashboard",
        path: "/dashboard",
        icon: LayoutDashboard,
    },
    {
        title: "Doctors",
        path: "/doctors",
        icon: FolderTree,
    },
    {
        title: "Appointments",
        path: "/appointments",
        icon: Package,
    },
    {
        title: "Logout",
        path: "/logout",
        icon: LogOut,
    },
];