import React from "react";
import {NavigationCategory} from "@tilpoe/react-navigation";

export const navigation: NavigationCategory[] = [
    {
        label: "Dashboard",
        items: [],
        path: "/dashboard"
    }, {
        label: "Produktion",
        items: [
            {
                label: "Schwarzes Regal",
                path: "/black-shelf"
            }
        ]
    }
];