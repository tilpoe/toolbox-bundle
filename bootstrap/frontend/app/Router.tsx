import React from "react";
import {Route, Routes} from "react-router-dom";
import {essentialRouter} from "@tilpoe/react-core";

export const router = {
    page: {
        index: "/"
    },
    api: {},
    essentials: {...essentialRouter}
}

export const Router = (): JSX.Element => {
    return (
        <Routes>
            <Route path={router.page.index} element={<div>Hello World!</div>}/>
        </Routes>
    );
}