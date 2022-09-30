/*
import React, {useEffect} from "react";
import {FormController, BasicFormController} from "@feierstoff/react-webpage-essentials/forms";
import {Checkbox, Input, Select, SelectItem, useSelect} from "@feierstoff/react-webpage-essentials/components";
import parse from "html-react-parser";
import {DateTime} from "@feierstoff/react-webpage-essentials/time";
import {useCore} from "@feierstoff/react-webpage-essentials/core";
import {Store, useAppDispatch} from "@store";

export interface AppointmentFormData {
    gender: string;
    firstname: string;
    lastname: string;
    birthday: string;
    street: string;
    housenumber: string;
    zip: string;
    city: string;
    phone: string;
    email: string;
    passnumber: string;
    cwa: string;
    agb: boolean;
    privacy: boolean;
    cwaOptionAnon: boolean;
    cwaOptionPersonal: boolean;
    testType: string;
    date: string;
    time: string;
}

const Gender = (props: BasicFormController<AppointmentFormData>): JSX.Element => {
    const select = useSelect([
        { text: "männlich", value: "M" },
        { text: "weiblich", value: "W" },
        { text: "divers", value: "D"}
    ]);

    return (
        <FormController
            {...props}
            label={"Geschlecht"}
            component={props => <Select select={select} {...props}/>}
            constraints={{
                required: "Bitte geben Sie das Geschlecht an."
            }}
        />
    );
};

const Firstname = (props: BasicFormController<AppointmentFormData>): JSX.Element => {
    return (
        <FormController
            {...props}
            label="Vorname"
            component={props => <Input {...props}/>}
            constraints={{
                required: "Geben Sie den Vornamen ein."
            }}
        />
    );
};

const Lastname = (props: BasicFormController<AppointmentFormData>): JSX.Element => {
    return (
        <FormController
            {...props}
            label="Nachname"
            component={props => <Input {...props}/>}
            constraints={{
                required: "Geben Sie den Nachnamen ein."
            }}
        />
    );
};

const Birthday = (props: BasicFormController<AppointmentFormData>): JSX.Element => {
    return (
        <FormController
            {...props}
            label="Geburtsdatum"
            component={props => <Input type="date" {...props}/>}
            constraints={{
                required: "Geben Sie das Geburtsdatum ein.",
                date: "Geben Sie ein gültiges Datum an."
            }}
        />
    );
};

const Street = (props: BasicFormController<AppointmentFormData>): JSX.Element => {
    return (
        <FormController
            {...props}
            label="Straße"
            component={props => <Input {...props}/>}
            constraints={{
                required: "Geben Sie die Straße ein."
            }}
        />
    );
};

const Housenumber = (props: BasicFormController<AppointmentFormData>): JSX.Element => {
    return (
        <FormController
            {...props}
            label="Hausnummer"
            component={props => <Input {...props}/>}
            constraints={{
                required: "Geben Sie die Hausnummer ein."
            }}
        />
    );
};

const Zip = (props: BasicFormController<AppointmentFormData>): JSX.Element => {
    return (
        <FormController
            {...props}
            label="Postleitzahl"
            component={props => <Input  {...props}/>}
            constraints={{
                required: "Geben Sie die Postleitzahl ein."
            }}
        />
    );
};

const City = (props: BasicFormController<AppointmentFormData>): JSX.Element => {
    return (
        <FormController
            {...props}
            label="Stadt"
            component={props => <Input {...props}/>}
            constraints={{
                required: "Geben Sie die Stadt ein."
            }}
        />
    );
};

const Email = (props: BasicFormController<AppointmentFormData>): JSX.Element => {
    return (
        <FormController
            {...props}
            label="E-Mail-Adresse"
            component={props => <Input {...props}/>}
            constraints={{
                required: "Geben Sie die E-Mail-Adresse ein.",
                email: "Geben Sie eine gültige E-Mail Adresse ein."
            }}
        />
    );
};

const Phone = (props: BasicFormController<AppointmentFormData>): JSX.Element => {
    return (
        <FormController
            {...props}
            label="Telefonnummer"
            component={props => <Input {...props}/>}
            constraints={{
                required: "Geben Sie die Telefonnummer ein."
            }}
        />  
    );
};

const Passnumber = (props: BasicFormController<AppointmentFormData>): JSX.Element => {
    return (
        <FormController
            {...props}
            label="Passnummer (optional)"
            component={props => <Input {...props}/>}
        />
    );
};

const SelectCwa = (props: BasicFormController<AppointmentFormData> & {
    setCwaState?: React.Dispatch<React.SetStateAction<number>>;
    disabled?: boolean;
}): JSX.Element => {
    const { setCwaState, disabled = false, ...other } = props;

    const setState = (state: number) => {
        if (setCwaState) setCwaState(state);
    };
    
    const select = useSelect([
        { text: "Keine Übermittlung", value: "0", onClick: () => setState(0)},
        { text: "Pseudonomysierte Übermittlung (Nicht-namentliche Anzeige)", value: "1", onClick: () => setState(1) },
        { text: "Personalisierte Übermittlung (Namentlicher Testnachweis)", value: "2", onClick: () => setState(2) }
    ], "0");
    
    return (
        <FormController
            {...other}
            label="Übermittlung an Corona-Warn-App"
            defaultValue={select.value}
            component={props => <Select select={select} disabled={disabled} {...props}/>}
        />  
    );
};

const CheckCwaOptionAnon = (props: BasicFormController<AppointmentFormData>): JSX.Element => {
    return (
        <FormController
            {...props}
            label="Einwilligung zur pseudonomisierten Übermittlung (Nicht-namentliche Anzeige)"
            component={props => <Checkbox info={parse(`
                Hiermit erkläre ich mein Einverständnis zum Übermitteln meines Testergebnisses und meines 
                pseudonymen Codes an das Serversystem des RKI, damit ich mein Testergebnis mit der Corona-Warn-App 
                abrufen kann. Das Testergebnis in der App kann hierbei nicht als namentlicher Testnachweis verwendet werden. 
                Die Hinweise zum Datenschutz finden Sie in Abschnitt 5 unserer <a href='https://deintestzentrum.de/datenschutz/' target="_blank">Datenschutzbestimmungen</a>.
            `)} {...props} />}
            constraints={{
                mandatory: {
                    msg: "Sie müssen Ihre Einwilligung geben."
                }
            }}
        />
    );
};

const CheckCwaOptionPersonal = (props: BasicFormController<AppointmentFormData>): JSX.Element => {
    return (
        <FormController
            {...props}
            label="Einwilligung zur personalisierten Übermittlung (Namentlicher Testnachweis)"
            component={props => <Checkbox info={
                parse(`
                    Hiermit erkläre ich mein Einverständnis zum Übermitteln des Testergebnisses und meines pseudonymen 
                    Codes an das Serversystem des RKI, damit ich mein Testergebnis mit der Corona-Warn-App abrufen kann. 
                    Ich willige außerdem in die Übermittlung meines Namens und Geburtsdatums an die App ein, damit mein 
                    Testergebnis in der App als namentlicher Testnachweis angezeigt werden kann. 
                    Die Hinweise zum Datenschutz finden Sie in Abschnitt 5 unserer <a href='https://deintestzentrum.de/datenschutz/' target="_blank">Datenschutzbestimmungen</a>.
                `)
            } {...props}/>}
            constraints={{
                mandatory: {
                    msg: "Sie müssen Ihre Einwilligung geben."
                }
            }}
        />
    );
};

const CheckAgb = (props: BasicFormController<AppointmentFormData>): JSX.Element => {
    return (
        <FormController
            {...props}
            label={<React.Fragment>Mit Ihrer Buchung erklären Sie sich mit unseren <a href='https://deintestzentrum.de/agb/' target={"_blank"} rel="noreferrer">AGB</a> einverstanden.</React.Fragment>}
            component={props => <Checkbox {...props}/>}
            constraints={{
                mandatory: {
                    msg: "Sie müssen den AGB zustimmen."
                }
            }}
        />
    );
};

const CheckPrivacy = (props: BasicFormController<AppointmentFormData>): JSX.Element => {
    return (
        <FormController
            {...props}
            label={<React.Fragment>Mit Ihrer Buchung erklären Sie sich mit unseren <a href={"https://deintestzentrum.de/datenschutz/"} target={"_blank"} rel="noreferrer">Datenschutzbestimmungen</a> einverstanden.</React.Fragment>}
            component={props => <Checkbox {...props}/>}
            constraints={{
                mandatory: {
                    msg: "Sie müssen den Datenschutzbestimmungen zustimmen."
                }
            }}
        />
    );
};

const Date = (props: BasicFormController<AppointmentFormData>): JSX.Element => {
    return (
        <FormController
            {...props}
            label={"Datum"}
            component={props => <Input type="date" {...props}/>}
            constraints={{
                required: "Bitte geben Sie ein Datum an.",
                date: "Geben Sie ein gültiges Datum an."
            }}
        />
    );
};

const Time = (props: BasicFormController<AppointmentFormData>): JSX.Element => {
    return (
        <FormController
            {...props}
            label={"Uhrzeit"}
            component={props => <Input type="time" {...props}/>}
            constraints={{
                required: "Bitte geben Sie eine Uhrzeit an.",
                time: "Geben Sie eine gültiges Uhrzeit an."
            }}
        />
    );
};

const TestType = (props: BasicFormController<AppointmentFormData> & {
    disabled: boolean;
    setValue?: (value?: string) => void;
}): JSX.Element => {
    const { disabled = false, setValue, ...other } = props;
    const core = useCore();
    const dispatch = useAppDispatch();

    const select = useSelect();

    useEffect(() => {
        async function fetchServices() {
            const services = core.callApi(
                await dispatch(Store.api.service.dispatch.get({})).unwrap()
            );

            if (!services) return;

            const selectItems: SelectItem[] = [];
            services.forEach((service) => {
                selectItems.push({ text: service.name, value: service.id.toString() });
            });

            if (selectItems && setValue) {
                setValue(selectItems[0].value);
            }
            select.setItems(selectItems);
        }

        fetchServices();
    }, []);

    return (
        <FormController
            {...other}
            label={"Service"}
            component={props => <Select disabled={disabled} select={select} {...props}/>}
            constraints={{
                required: "Bitte geben Sie den gewünschten Service an."
            }}
        />
    );
};

export default {
    Firstname,
    Gender,
    Lastname,
    Birthday,
    Street,
    Housenumber,
    Zip,
    City,
    Email,
    Phone,
    Passnumber,
    SelectCwa,
    CheckCwaOptionAnon,
    CheckCwaOptionPersonal,
    CheckAgb,
    CheckPrivacy,
    Date,
    Time,
    TestType
};*/
