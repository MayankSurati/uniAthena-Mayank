import { useLocation } from "react-router-dom";

export default function Breadcrumb() {
    const { pathname } = useLocation();

    const segments = pathname
        .split("/")
        .filter(Boolean);

    return (
        <div className="text-gray-500 text-sm mb-4">
            Home
            {segments.map((segment) => (
                <span key={segment}>
                    {" / "}
                    {segment}
                </span>
            ))}
        </div>
    );
}