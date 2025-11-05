import express from "express"
import smsRouter from "./sms.js"

const router = express.Router()

router.use("/sms", smsRouter) 

// for payment
router.use("/", )


export default router;

