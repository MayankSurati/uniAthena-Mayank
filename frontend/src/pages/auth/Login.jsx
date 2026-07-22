import { useForm } from "react-hook-form";
import { useNavigate } from "react-router-dom";
import api from "../../api/interceptors";
import { useAuth } from "../../contexts/AuthContext";

export default function Login() {
    const { register, handleSubmit } = useForm();
    const navigate = useNavigate();
    const { login } = useAuth();

    const onSubmit = async (data) => {
        try {
            const response = await api.post("/auth/login", data);

            const token = response.data.data.token;
            const user = response.data.data.user;

            login(token, user);

            navigate("/dashboard");
        } catch (error) {
            alert(error.response?.data?.message ?? "Login failed.");
        }
    };

    return (
        <div className="min-h-screen flex items-center justify-center">
            <div className="w-96 p-6 border rounded-lg shadow">
                <form onSubmit={handleSubmit(onSubmit)}>
                    <input className="border w-full p-2 mb-3" name="email" type="email"
                        {...register("email")}
                        placeholder="Email"
                    />

                    <input className="border w-full p-2 mb-3" name="password" type="password"
                        {...register("password")}
                        placeholder="Password"
                    />

                    <button type="submit" className="bg-blue-600 text-white w-full py-2 rounded">
                        Login
                    </button>
                </form>
            </div>
        </div>
    );
}