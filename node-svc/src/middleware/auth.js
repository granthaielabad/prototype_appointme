import dotenv from "dotenv"


export function requireInternalToken(req, res, next ) {

    const token = req.get("X-Internal-Token");
    console.log("Token from postman", token)
    console.log("Token from dotenv" ,process.env.INTERNAL_API_TOKEN)

    if (!token || token  !== process.env.INTERNAL_API_TOKEN){
        return res.status(401).json({error:"Unauthorized"})
    }
    
    next();

}