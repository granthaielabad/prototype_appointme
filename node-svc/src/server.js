import express from "express"
import dotenv from "dotenv"
import cors from "cors"
import { requireInternalToken } from "./middleware/auth.js";
import routes from "./routes/index.js"


dotenv.config()
console.log("Is the token being loaded.", process.env.INTERNAL_API_TOKEN )

const app = express();

app.use(express.json());

app.use(cors({origin:false}));

app.get("/health", (req, res) => {
  res.json({ status: "healthy" });
});

app.use("/api", requireInternalToken);

app.use("/api", routes )

app.use((err, req, res, next) => {
  console.error(" Unhandled Error:", err);
  const status = err.statusCode || 500;
  res.status(status).json({
    success: false,
    message: err.message,
    cause: err.cause?.message,
  });
});

const PORT = 4000;
app.listen(PORT, () => {
  console.log(` Node service running on port ${PORT}`);
});





