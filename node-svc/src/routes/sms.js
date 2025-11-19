import express from "express"
import { requireInternalToken } from "../middleware/auth.js";
import { sendSMS } from "../services/philsms.js";


const router = express.Router();

router.use(requireInternalToken);

router.post("/" , async (req , res , next  ) => {

    try {
        const { recipient, message , sender_id /* "sender_id" For future purposes kung may sarili ng sender_id ang company */  } = req.body
            


            console.log(" SMS.JS = Recipient: " , recipient, "Message: " , message , "senderId: ", sender_id)


            //validation ko
            if (!recipient || !message) {
               return res.status(400).json({ error: "Recipient and Message column are both needed."});

            };

            const result = await sendSMS(recipient, message, sender_id);
            res.json({ success: true , data: result });


    } catch (error) {

        next(error)


    };


});

export default router;










