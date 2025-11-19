import express from "express"
import smsRouter from "./sms.js"
import paymentsRouter from "./payment.js"

const router = express.Router()

router.use("/sms", smsRouter) 

router.use("/payments", paymentsRouter)




export default router;

