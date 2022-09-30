#!/bin/bash

read -p "Name of the new page (kebab-case): " name_keb

touch camelize.js
cat <<EOT >> camelize.js
function camelize(s) {
  return s.replace(/-./g, x=>x[1].toUpperCase());
}

function capitalizeFirstLetter(string) {
  return string.charAt(0).toUpperCase() + string.slice(1);
}

const name_keb = "${name_keb}";
const name_camel = capitalizeFirstLetter(camelize(name_keb));
console.log(name_camel);
EOT
name_camel=$(node camelize.js)

rm camelize.js
cd frontend/pages

mkdir ${name_keb}
cd ${name_keb}
touch index.tsx
mkdir routes
mkdir routes/index

cat <<EOT >> index.tsx
import React from "react";
import { RouteConfig } from "@feierstoff/react-webpage-essentials/routes";

import ${name_camel} from "./routes/index/${name_camel}";

const ${name_camel}Page: RouteConfig[] = [
    {
        path: "${name_keb}",
        component: <${name_camel}/>
    }
];

export default ${name_camel}Page;
EOT

cd routes/index
touch ${name_camel}.tsx

cat <<EOT >> ${name_camel}.tsx
import React from "react";

const ${name_camel} = (): JSX.Element => {
  return (
    <div>test</div>
  );
};

export default ${name_camel}
EOT

echo ""
echo "!!! ADD PAGE TO app/routes.ts !!!"
