import { defineConfig, loadEnv } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), "");

    const host = env.VITE_HOST || "localhost";

    return {
        server: {
            host: "0.0.0.0",
            port: 5173,

            // habilita CORS
            cors: true,

            // origem anunciada pelo Vite
            origin: `http://${host}`,

            // configuração do HMR
            hmr: {
                host,
                protocol: "ws",
                port: 5173,
            },

            // cabeçalhos explícitos
            headers: {
                "Access-Control-Allow-Origin": "*",
            },
        },

        plugins: [
            laravel({
                input: ["resources/css/app.css", "resources/js/app.js"],
                refresh: true,
            }),
        ],
    };
});
