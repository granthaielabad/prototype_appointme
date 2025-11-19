import express from "express"
import dotenv from "dotenv"
import cors from "cors"
import routes from "./routes/index.js"


dotenv.config()


const app = express();


app.use(express.json()); 

// para sa webhook
app.use((req, res, next) => {
  if (req.path === "/api/payments/webhook") {
    let chunks = [];
    req.on("data", (c) => chunks.push(c));
    req.on("end", () => {
      req.rawBody = Buffer.concat(chunks);
      try {
        req.body = JSON.parse(req.rawBody.toString("utf8"));
      } catch {
        req.body = {};
      }
      next();
    });
  } else {
    express.json({ limit: "1mb" })(req, res, next);
  }
});


app.use(cors({ origin: true , credentials: false })); 

app.get("/health" , ( req, res) => {
   res.json({ status: "healthy" }); 
} )



app.use("/api", routes)


// error handler for globally ng mga files. 
app.use((err, req, res, next) => { 
  console.error("Unhandled Error:", err);

  const status = err.statusCode || 500
 

  res.status(status).json({
    success: false,
    message: err.message,
    cause: err.cause?.message
  })

})


const PORT = process.env.PORT ||  4000; 
app.listen(PORT, ()=> {
  console.log("Running Server on 4000")
})



