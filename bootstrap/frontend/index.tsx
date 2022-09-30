import "./styles/app.scss";

import React from "react";
import {navigation, Router} from "@app";
import {createRoot} from "react-dom/client";
import {Application} from "@tilpoe/react-core";

const Root = (): JSX.Element => {
    return (
        <Application navigation={navigation} routes={<Router/>}/>
    );
};

const container = document.getElementById("app");
const root = createRoot(container!);
root.render(<Root/>);