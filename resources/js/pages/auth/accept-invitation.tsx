import InputError from '@/components/input-error';
import TextLink from '@/components/text-link';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Spinner } from '@/components/ui/spinner';
import AuthLayout from '@/layouts/auth-layout';
import { login } from '@/routes';
import type { Invitation } from '@/types';
import { Form, Head, router, usePage } from '@inertiajs/react';
import { Building2, Mail, Shield } from 'lucide-react';

interface AcceptInvitationProps {
    invitation: Invitation;
    token: string;
    hasAccount: boolean;
}

export default function AcceptInvitation({
    invitation,
    token,
    hasAccount,
}: AcceptInvitationProps) {
    const { auth } = usePage<{ auth?: { user?: { email: string } } }>().props;
    const isAuthenticated = !!auth?.user;
    const isCorrectUser = auth?.user?.email === invitation.email;

    // Case 1: User needs to create an account
    if (!hasAccount) {
        return (
            <AuthLayout
                title="Join organisation"
                description={`You've been invited to join ${invitation.organization.name}`}
            >
                <Head title="Accept Invitation" />

                <Card>
                    <CardHeader>
                        <CardTitle className="flex items-center gap-2">
                            <Building2 className="size-5 text-primary" />
                            {invitation.organization.name}
                        </CardTitle>
                        <CardDescription>
                            Create your account to accept this invitation
                        </CardDescription>
                    </CardHeader>

                    <CardContent className="space-y-4">
                        <div className="space-y-3">
                            <div className="flex items-start gap-3">
                                <Mail className="mt-0.5 size-4 text-muted-foreground" />
                                <div className="space-y-1">
                                    <p className="text-sm font-medium">
                                        Email address
                                    </p>
                                    <p className="text-sm text-muted-foreground">
                                        {invitation.email}
                                    </p>
                                </div>
                            </div>

                            {invitation.roles.length > 0 && (
                                <div className="flex items-start gap-3">
                                    <Shield className="mt-0.5 size-4 text-muted-foreground" />
                                    <div className="space-y-1">
                                        <p className="text-sm font-medium">
                                            Role
                                            {invitation.roles.length > 1
                                                ? 's'
                                                : ''}
                                        </p>
                                        <p className="text-sm text-muted-foreground">
                                            {invitation.roles.join(', ')}
                                        </p>
                                    </div>
                                </div>
                            )}
                        </div>

                        <Alert>
                            <AlertDescription>
                                We couldn't find an account with this email
                                address. Create your account to continue.
                            </AlertDescription>
                        </Alert>

                        <Button
                            className="w-full"
                            onClick={() =>
                                router.visit(
                                    `/invitations/accept/${token}/register`,
                                )
                            }
                        >
                            Create account
                        </Button>
                    </CardContent>
                </Card>
            </AuthLayout>
        );
    }

    // Case 2: User is not logged in but has an account
    if (!isAuthenticated) {
        return (
            <AuthLayout
                title="Sign in to continue"
                description={`You've been invited to join ${invitation.organization.name}`}
            >
                <Head title="Accept Invitation" />

                <Card>
                    <CardHeader>
                        <CardTitle className="flex items-center gap-2">
                            <Building2 className="size-5 text-primary" />
                            {invitation.organization.name}
                        </CardTitle>
                        <CardDescription>
                            Sign in to accept this invitation
                        </CardDescription>
                    </CardHeader>

                    <CardContent className="space-y-4">
                        <div className="space-y-3">
                            <div className="flex items-start gap-3">
                                <Mail className="mt-0.5 size-4 text-muted-foreground" />
                                <div className="space-y-1">
                                    <p className="text-sm font-medium">
                                        Email address
                                    </p>
                                    <p className="text-sm text-muted-foreground">
                                        {invitation.email}
                                    </p>
                                </div>
                            </div>

                            {invitation.roles.length > 0 && (
                                <div className="flex items-start gap-3">
                                    <Shield className="mt-0.5 size-4 text-muted-foreground" />
                                    <div className="space-y-1">
                                        <p className="text-sm font-medium">
                                            Role
                                            {invitation.roles.length > 1
                                                ? 's'
                                                : ''}
                                        </p>
                                        <p className="text-sm text-muted-foreground">
                                            {invitation.roles.join(', ')}
                                        </p>
                                    </div>
                                </div>
                            )}
                        </div>

                        <Alert>
                            <AlertDescription>
                                Please sign in with your account to accept this
                                invitation.
                            </AlertDescription>
                        </Alert>

                        <Button
                            className="w-full"
                            onClick={() =>
                                router.visit(
                                    login(invitation.organization.slug).url,
                                    {
                                        data: {
                                            redirect: `/invitations/accept/${token}`,
                                        },
                                    },
                                )
                            }
                        >
                            Sign in to continue
                        </Button>
                    </CardContent>
                </Card>
            </AuthLayout>
        );
    }

    // Case 3: User is logged in but with wrong account
    if (!isCorrectUser) {
        return (
            <AuthLayout
                title="Wrong account"
                description={`You've been invited to join ${invitation.organization.name}`}
            >
                <Head title="Accept Invitation" />

                <Card>
                    <CardHeader>
                        <CardTitle className="flex items-center gap-2">
                            <Building2 className="size-5 text-primary" />
                            {invitation.organization.name}
                        </CardTitle>
                        <CardDescription>
                            This invitation is for a different account
                        </CardDescription>
                    </CardHeader>

                    <CardContent className="space-y-4">
                        <Alert variant="destructive">
                            <AlertDescription>
                                This invitation is for{' '}
                                <strong>{invitation.email}</strong>, but you're
                                signed in as <strong>{auth.user.email}</strong>.
                                Please sign out and sign in with the correct
                                account.
                            </AlertDescription>
                        </Alert>

                        <div className="flex gap-2">
                            <Button
                                variant="outline"
                                className="flex-1"
                                onClick={() => router.post('/logout')}
                            >
                                Sign out
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </AuthLayout>
        );
    }

    // Case 4: User is authenticated with correct account - show acceptance form
    return (
        <AuthLayout
            title="Accept invitation"
            description={`You've been invited to join ${invitation.organization.name}`}
        >
            <Head title="Accept Invitation" />

            <Form
                action={`/invitations/accept/${token}`}
                method="post"
                className="flex flex-col gap-6"
            >
                {({ processing, errors }) => (
                    <>
                        <Card>
                            <CardHeader>
                                <CardTitle className="flex items-center gap-2">
                                    <Building2 className="size-5 text-primary" />
                                    {invitation.organization.name}
                                </CardTitle>
                                <CardDescription>
                                    Review your invitation details
                                </CardDescription>
                            </CardHeader>

                            <CardContent className="space-y-4">
                                <div className="space-y-3">
                                    <div className="flex items-start gap-3">
                                        <Mail className="mt-0.5 size-4 text-muted-foreground" />
                                        <div className="space-y-1">
                                            <p className="text-sm font-medium">
                                                Email address
                                            </p>
                                            <p className="text-sm text-muted-foreground">
                                                {invitation.email}
                                            </p>
                                        </div>
                                    </div>

                                    {invitation.roles.length > 0 && (
                                        <div className="flex items-start gap-3">
                                            <Shield className="mt-0.5 size-4 text-muted-foreground" />
                                            <div className="space-y-1">
                                                <p className="text-sm font-medium">
                                                    Role
                                                    {invitation.roles.length > 1
                                                        ? 's'
                                                        : ''}
                                                </p>
                                                <p className="text-sm text-muted-foreground">
                                                    {invitation.roles.join(
                                                        ', ',
                                                    )}
                                                </p>
                                            </div>
                                        </div>
                                    )}

                                    {invitation.inviter && (
                                        <div className="text-sm text-muted-foreground">
                                            Invited by {invitation.inviter.name}
                                        </div>
                                    )}
                                </div>

                                <InputError message={errors.token} />
                                <InputError message={errors.email} />

                                <Button
                                    type="submit"
                                    className="w-full"
                                    disabled={processing}
                                    data-test="accept-invitation-button"
                                >
                                    {processing && <Spinner />}
                                    Accept invitation
                                </Button>
                            </CardContent>
                        </Card>

                        <div className="text-center text-sm text-muted-foreground">
                            Wrong account?{' '}
                            <TextLink
                                href="#"
                                onClick={(e) => {
                                    e.preventDefault();
                                    router.post('/logout');
                                }}
                            >
                                Sign out
                            </TextLink>
                        </div>
                    </>
                )}
            </Form>
        </AuthLayout>
    );
}
