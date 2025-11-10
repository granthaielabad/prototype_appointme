import { createCheckoutSession } from "../services/paymongo.js";



const SUCCESS_URL = (process.env.SUCCESS_URL)
const CANCEL_URL = (process.env.CANCEL_URL)

export async function startCheckout(req, res, next){

    try {
        
        const {billing, line_items, reference_number } = req.body;

        console.log(billing, line_items, reference_number)

        const session = await createCheckoutSession({
            billing, 
            line_items,
            cancel_url: CANCEL_URL ,
            success_url: SUCCESS_URL ,
            reference_number,
        })

        res.json({ success: true , checkout_url : session.checkout_url, payment_intent_id: session.payment_intent_id, reference_number  
        })

    


    } catch (error) {
        console.error("PayMong Error", error.message);
        res.status(500).json({ success: false, message:error.message});

        // next(err) replace the catch kapag hindi gumana.


        

    }





}