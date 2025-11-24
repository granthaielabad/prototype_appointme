import axios from "axios"
import dotenv from "dotenv"

dotenv.config();

const apiTokken = (process.env.PHILSMS_API_TOKEN);
const apiUrl = (process.env.PHILSMS_API_URL);

console.log("PHILSMS- TOKEN ", apiTokken)
console.log("PHILSMS- URL ", apiUrl)


function toPhilippinesInternational(recipient) {
  if (!recipient) return recipient;

  // remove spaces, dashes, etc.
  let digits = recipient.replace(/\D/g, '');

  // If it starts with 0 and has 11 digits → convert 09xxxxxxxxx → 639xxxxxxxxx
  if (digits.length === 11 && digits.startsWith('0')) {
    digits = '63' + digits.slice(1);
  }

  // If it already starts with 63, keep as is
  // (e.g. 639567915563)
  return digits;
}



export async function sendSMS (recipient, message, senderId = "PhilSMS") {
    
    try { 

        const normalizedRecipient = toPhilippinesInternational(recipient);
        console.log("philsms.js recipient" +normalizedRecipient)

        const payLoad = {
            recipient: normalizedRecipient,
            sender_id: senderId,
            type: "plain",
            message,
        }

        const response = await axios.post (apiUrl, payLoad, {
            headers: {
                'Authorization' : `Bearer ${apiTokken}`,
                'Content-Type' : 'application/json',
                'Accept': 'application/json',
            },
        }) ;

        const result = await response.data;

        if (result.status !== "success") {
            throw new Error (`SMS failed: ${JSON.stringify(result)}`);
        }

        console.log('SMS send Succestfully:', result);

        return result // himayin ko mamaya anong need na data.
        
    } catch (error) {
        console.error('Error Found:',error.message)
        throw error;
    }

}