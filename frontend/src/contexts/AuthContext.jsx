import { createContext, useContext, useState } from "react";
import { logoutApi } from "../api/auth";

const AuthContext = createContext();

export function AuthProvider({ children }) {
    const [user, setUser] = useState(null);
    const [loading, setLoading] = useState(false);

    const login = (token, user) => {
        localStorage.setItem("token", token);
        setUser(user);
    };

    const logout = async () => {
        setLoading(true);
        try {
            await logoutApi();
        } catch (error) {
            console.error("Logout API failed:", error);
        } finally {
            localStorage.removeItem("token");
            sessionStorage.removeItem("token");

            setUser(null);
        }
    };

    return (
        <AuthContext.Provider
            value={{
                user,
                login,
                logout,
                loading,
            }}
        >
            {children}
        </AuthContext.Provider>
    );
}

export function useAuth() {
    return useContext(AuthContext);
}