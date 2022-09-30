/*
import {QrCode} from "@feierstoff/react-webpage-essentials/qr";

export interface QrCwaVCardType {
    firstname: string;
    lastname: string;
    birthday: string;
    email: string;
    phone: string;
    street: string;
    housenumber: string;
    zip: string;
    city: string;
}

export default class QrCwaVCard extends QrCode<QrCwaVCard, QrCwaVCardType> {
    public QR_TYPE = "qr-cwa-card";

    isTypeOf(data: any): data is QrCwaVCardType {
        data = this.parseJson(data);

        if (typeof data === "object") {
            if (
                typeof data?.firstname == "string" &&
                typeof data?.lastname == "string" &&
                typeof data?.birthday == "string" &&
                typeof data?.email == "string" &&
                typeof data?.phone == "string" &&
                typeof data?.street == "string" &&
                typeof data?.housenumber == "string" &&
                typeof data?.zip == "string" &&
                typeof data?.city == "string"
            ) return true;
        }

        return false;
    }
}*/
