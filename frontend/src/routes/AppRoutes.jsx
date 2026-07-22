import {
    BrowserRouter,
    Routes,
    Route
} from "react-router-dom";

import Login from "../pages/auth/Login";
import Dashboard from "../pages/dashboard/Dashboard";

import AdminLayout from "../layouts/AdminLayout";
import AuthLayout from "../layouts/AuthLayout";

import ProtectedRoute from "./ProtectedRoute";

export default function AppRoutes() {

    return (

        <BrowserRouter>

            <Routes>

                <Route element={<AuthLayout />}>

                    <Route
                        path="/login"
                        element={<Login />}
                    />

                </Route>

                <Route
                    path="/"
                    element={
                        <ProtectedRoute>

                            <AdminLayout />

                        </ProtectedRoute>
                    }
                >

                    <Route
                        path="dashboard"
                        element={<Dashboard />}
                    />

                </Route>

            </Routes>

        </BrowserRouter>

    );
}