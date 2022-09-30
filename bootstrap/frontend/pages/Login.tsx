/*
import React from "react";
import {Button, Card, CardContent, Flex, Page, Input} from "@tilpoe/quick-ui";
import {FormController, useForm} from "@tilpoe/react-essentials/forms";
import {callMutation, RESPONSE_UNAUTHORIZED, useLogin} from "@tilpoe/react-essentials/api";
import {toast} from "react-toastify";

export const Login = (): JSX.Element => {
    const login = useLogin();

    const { errors, ...form } = useForm<{
        username: string;
        password: string;
    }>("login", async (data) => {
        await callMutation(login, {
            username: data.username,
            password: data.password
        }, {
            handleError: (err) => {
                switch (err.code) {
                    case RESPONSE_UNAUTHORIZED:
                        toast.error("Fehlerhafte Zugangsdaten.");
                        form.setValue("password", "");
                        break;
                    default:
                        toast.error("Es ist ein Fehler aufgetreten.");
                }
            }
        });
    });

    return (
        <Page>
            <Flex grow={1} justify={"center"} align={"center"}>
                <form {...form.formProps}>
                    <Card maxWidth={"400px"}>
                        <CardContent>
                            <Flex direction={"column"} space={3}>
                                <FormController
                                    {...form.controlProps("username", "")}
                                    label={"Nutzername"}
                                    component={props => <Input {...props}/>}
                                    constraints={{
                                        required: "Gib deinen Benutzernamen ein."
                                    }}
                                />
                                <FormController
                                    {...form.controlProps("password", "")}
                                    label={"Passwort"}
                                    component={props => <Input type={"password"} {...props}/>}
                                    constraints={{
                                        required: "Gib dein Passwort ein."
                                    }}
                                />
                                <Button
                                    text="Anmelden"
                                    submitForm={form.id}
                                    disabled={login.isLoading}
                                />
                            </Flex>
                        </CardContent>
                    </Card>
                </form>
            </Flex>
        </Page>
    );
}*/
