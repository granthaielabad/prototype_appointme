// middleware/auth.js
export function requireInternalToken(req, res, next) {
  // In Express, headers are lowercased. Use req.get() or req.headers.authorization
  const authHeader = req.get("authorization") || ""; // e.g. "Bearer abc123" or "" if missing

  // Accept either "Bearer <token>" or just "<token>"
  let provided = authHeader.trim();
  if (provided.toLowerCase().startsWith("bearer ")) {
    provided = provided.slice(7).trim();
  }

  const expected = process.env.INTERNAL_API_TOKEN;

  // Helpful debug (keep while testing)
  console.log("Token from postman", provided || "undefined");
  console.log("Token from dotenv", expected || "undefined");

  if (!expected) {
    return res.status(500).json({ error: "Server misconfigured: INTERNAL_API_TOKEN is missing" });
  }

  if (!provided || provided !== expected) {
    return res.status(401).json({ error: "Unauthorized" });
  }

  return next();
}
