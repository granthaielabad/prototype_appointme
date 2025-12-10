import { createCheckoutSession } from "../services/paymongo.js";

export async function startCheckout(req, res) {
  try {
    const {
      billing,
      line_items,
      reference_number,
      success_url,
      cancel_url,
    } = req.body;

    const resolvedSuccess = success_url || process.env.SUCCESS_URL;
    const resolvedCancel = cancel_url || process.env.CANCEL_URL;

    const session = await createCheckoutSession({
      billing,
      line_items,
      cancel_url: resolvedCancel,
      success_url: resolvedSuccess,
      reference_number,
    });

    res.json({
      success: true,
      checkout_url: session.checkout_url,
      payment_intent_id: session.payment_intent_id,
      reference_number,
    });
  } catch (error) {
    console.error("PayMongo Error", error.message);
    res.status(500).json({ success: false, message: error.message });
  }
}
