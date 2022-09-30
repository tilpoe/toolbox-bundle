const fs = require("fs");
const path = require("path");
require("dotenv").config({
    path: path.resolve(process.cwd(), ".env.local")
});

var composer = require("../composer-prod.json");
composer.repositories[0].url = process.env.TOOLBOX_PATH;
composer.require["feierstoff/toolbox-bundle"] = "dev-master";

fs.writeFileSync("composer-prod.json", JSON.stringify(composer, null, 2));