import express from "express"
import { sendSMS } from "../services/philsms.js";

const router = express.Router()

router.post("/", async (req, res, next) => {
    
    try {
        
        const { recipient, message, senderId } = req.body;

        console.log("(SMS.JS) Recipient", recipient,"Message" , message,"senderID", senderId)

        if (!recipient || ! message ) {
            return res.status(400).json({error:"Recipient and message are required." });
        }

        const result = await sendSMS(recipient, message, senderId);

        res.json({ success: true, data: result});


    } catch (error) {

        next(error);
    }

});

export default router;
    

