import express from "express"
import { requireInternalToken } from "../middleware/auth.js";
import { startCheckout } from "../controller/payment_controller.js";


console.log("✅ payment.js loaded");


const router = express.Router(); // what is the difference const router = Router();



router.post("/payments/checkout", requireInternalToken, startCheckout )


// don't mind  this. 
// router.post("/payments/webhook", handleWebhook  )



export default router;

