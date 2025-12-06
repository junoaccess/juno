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
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AuthLayout from '@/layouts/auth-layout';
import { login } from '@/routes';
import type { Invitation } from '@/types';
import { Form, Head } from '@inertiajs/react';
import { Building2, Info } from 'lucide-react';

interface RegisterWithInvitationProps {
    invitation: Invitation;
    token: string;
}

export default function RegisterWithInvitation({
    invitation,
    token,
}: RegisterWithInvitationProps) {
    return (
        <AuthLayout
            title="Create your account"
            description={`Join ${invitation.organization.name} to get started`}
        >
            <Head title="Create Account" />

            <Form
                action={`/invitations/accept/${token}`}
                method="post"
                resetOnSuccess={['password', 'password_confirmation']}
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
                                    You've been invited to join this organisation
                                </CardDescription>
                            </CardHeader>

                            <CardContent className="space-y-4">
                                <Alert>
                                    <Info className="size-4" />
                                    <AlertDescription>
                                        {invitation.name
                                            ? `${invitation.name}, you`
                                            : 'You'}'ve been invited to join{' '}
                                        {invitation.organization.name}
                                        {invitation.roles.length > 0 &&
                                            ` as ${invitation.roles.join(', ')}`}
                                        .
                                    </AlertDescription>
                                </Alert>

                                <div className="grid gap-4">
                                    <div className="grid gap-2">
                                        <Label htmlFor="first_name">
                                            First name
                                        </Label>
                                        <Input
                                            id="first_name"
                                            type="text"
                                            name="first_name"
                                            required
                                            autoFocus
                                            tabIndex={1}
                                            autoComplete="given-name"
                                            placeholder="John"
                                            defaultValue={
                                                invitation.name?.split(' ')[0] ||
                                                ''
                                            }
                                        />
                                        <InputError
                                            message={errors.first_name}
                                        />
                                    </div>

                                    <div className="grid gap-2">
                                        <Label htmlFor="last_name">
                                            Last name
                                        </Label>
                                        <Input
                                            id="last_name"
                                            type="text"
                                            name="last_name"
                                            required
                                            tabIndex={2}
                                            autoComplete="family-name"
                                            placeholder="Doe"
                                            defaultValue={
                                                invitation.name
                                                    ?.split(' ')
                                                    .slice(1)
                                                    .join(' ') || ''
                                            }
                                        />
                                        <InputError message={errors.last_name} />
                                    </div>

                                    <div className="grid gap-2">
                                        <Label htmlFor="email">
                                            Email address
                                        </Label>
                                        <Input
                                            id="email"
                                            type="email"
                                            name="email"
                                            required
                                            readOnly
                                            tabIndex={-1}
                                            value={invitation.email}
                                            className="bg-muted"
                                        />
                                        <p className="text-sm text-muted-foreground">
                                            This email is from your invitation and
                                            cannot be changed.
                                        </p>
                                        <InputError message={errors.email} />
                                    </div>

                                    <div className="grid gap-2">
                                        <Label htmlFor="password">Password</Label>
                                        <Input
                                            id="password"
                                            type="password"
                                            name="password"
                                            required
                                            tabIndex={3}
                                            autoComplete="new-password"
                                            placeholder="Create a password"
                                        />
                                        <InputError message={errors.password} />
                                    </div>

                                    <div className="grid gap-2">
                                        <Label htmlFor="password_confirmation">
                                            Confirm password
                                        </Label>
                                        <Input
                                            id="password_confirmation"
                                            type="password"
                                            name="password_confirmation"
                                            required
                                            tabIndex={4}
                                            autoComplete="new-password"
                                            placeholder="Confirm your password"
                                        />
                                        <InputError
                                            message={
                                                errors.password_confirmation
                                            }
                                        />
                                    </div>
                                </div>

                                <InputError message={errors.token} />

                                <Button
                                    type="submit"
                                    className="w-full"
                                    tabIndex={5}
                                    disabled={processing}
                                    data-test="create-account-button"
                                >
                                    {processing && <Spinner />}
                                    Create account and join
                                </Button>
                            </CardContent>
                        </Card>

                        <div className="text-center text-sm text-muted-foreground">
                            Already have an account?{' '}
                            <TextLink
                                href={login(invitation.organization.slug).url}
                                tabIndex={6}
                            >
                                Sign in
                            </TextLink>
                        </div>
                    </>
                )}
            </Form>
        </AuthLayout>
    );
}
