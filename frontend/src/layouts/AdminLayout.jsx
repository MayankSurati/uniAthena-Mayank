import { Outlet } from "react-router-dom";
import Sidebar from "../components/layout/Sidebar";
import Header from "../components/layout/Header";
import Footer from "../components/layout/Footer";
import Breadcrumb from "../components/layout/Breadcrumb";

export default function AdminLayout() {
    return (
        <div className="flex min-h-screen bg-gray-100">
            <Sidebar />

            <div className="flex flex-col flex-1">
                <Header />

                <main className="flex-1 p-6">
                    <Breadcrumb />
                    <Outlet />
                </main>

                <Footer />
            </div>
        </div>
    );
}