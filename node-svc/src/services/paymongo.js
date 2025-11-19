 import axios from 'axios';

const PAYMONGO_API = "https://api.paymongo.com/v1"
const PAYMONGO_TOKEN = process.env.PAYMONGO_SECRET_KEY  

console.log("Paymongo token ", PAYMONGO_TOKEN)


function authHeader(){
    const token = Buffer.from(`${PAYMONGO_TOKEN}:`).toString("base64");
     console.log("Auth header preview:", `Basic ${token.slice(0,12)}...`);
    return { Authorization: `Basic ${token}`};
    
   
}




// I  will ad later the idemptency..

export function  idempotencyHeaders(key) {
    return key ? {"Idempotency-Key": key} : {};
}

export async function createCheckoutSession({billing,  line_items, cancel_url, success_url, reference_number, show_description = true,    show_line_items = true, idemptencyKey }){


    


    try {

        if (!billing?.email || !billing?.name ) {
        throw new Error ("Billing name and Email are Required")
    }
  
    if (!Array.isArray(line_items) || line_items.length === 0) {
        throw new Error("At least one line item is required")
    }


    const payload = {
        data: {
            attributes: {
                billing, //
                line_items,//
                payment_method_types: ["gcash"],
                send_email_receipt: !!billing.email,
                show_line_items,
                cancel_url,
                success_url,
                reference_number    

            },
        },
    };

    const headers = {
        ...authHeader(),
        "Content-Type" : "application/json",
        ...idempotencyHeaders(idemptencyKey)
    }

    const result = await axios.post(`${PAYMONGO_API}/checkout_sessions`, payload, {headers});

    return result.data.data.attributes;
        
    } catch (error) {
        
        const status = error?.response?.status;
        const data   = error?.response?.data;
        console.error("PayMongo create checkout failed:", status, JSON.stringify(data, null, 2));
        throw error; 


    }
}













