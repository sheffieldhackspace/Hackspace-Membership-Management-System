import UserData = App.Data.UserData;

export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    auth: {
        user: UserData;
        permissions: string[];
    };
};
