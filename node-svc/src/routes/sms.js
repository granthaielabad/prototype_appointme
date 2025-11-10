import express from "express"
import { requireInternalToken } from "../middleware/auth.js";
import { sendSMS } from "../services/philsms.js";


const router = express.Router();

router.use(requireInternalToken);

router.post("/" , async (req , res , next  ) => {

    try {
        const { recipient, message , senderId /* sender_id try ko to mamaya this if it work then used it. */ } = req.body
            


            console.log("Recipient: " , recipient, "Message: " , message , "senderId: ", senderId)


            //validation ko
            if (!recipient || !message) {
               return res.status(400).json({ error: "Recipient and Message column are both needed."});

            };

            const result = await sendSMS(recipient, message, senderId);
            res.json({ success: true , data: result });


           



    } catch (error) {

        next(error)


    };


});

export default router;

// Developer mindset:“Never res.json() inside a catch block. Always delegate to the centralized error handler.” why ??









