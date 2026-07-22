import api from "./interceptors";

export const loginApi = (data) => api.post("/auth/login", data);

export const userApi = () => api.get("/auth/user");

export const logoutApi = () => api.post("/auth/logout");