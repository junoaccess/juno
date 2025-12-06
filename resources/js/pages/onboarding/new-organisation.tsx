import InputError from '@/components/input-error';
import TextLink from '@/components/text-link';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Separator } from '@/components/ui/separator';
import { Spinner } from '@/components/ui/spinner';
import AuthLayout from '@/layouts/auth-layout';
import { login } from '@/routes';
import { Form, Head } from '@inertiajs/react';
import { useEffect, useState } from 'react';

interface NewOrganisationProps {
    mainDomain: string;
}

export default function NewOrganisation({ mainDomain }: NewOrganisationProps) {
    const [organisationName, setOrganisationName] = useState('');
    const [slug, setSlug] = useState('');
    const [slugTouched, setSlugTouched] = useState(false);

    // Auto-generate slug from organisation name
    useEffect(() => {
        if (!slugTouched && organisationName) {
            const generatedSlug = organisationName
                .toLowerCase()
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '');
            setSlug(generatedSlug);
        }
    }, [organisationName, slugTouched]);

    return (
        <AuthLayout
            title="Create your organisation"
            description="Get started by setting up your organisation and owner account"
        >
            <Head title="Create Organisation" />

            <Form
                action="/onboarding/organisation"
                method="post"
                resetOnSuccess={['password', 'password_confirmation']}
                className="flex flex-col gap-6"
            >
                {({ processing, errors }) => (
                    <>
                        <div className="grid gap-6">
                            {/* Organisation Details Section */}
                            <div className="space-y-4">
                                <div className="space-y-1">
                                    <h2 className="text-sm font-medium">
                                        Organisation details
                                    </h2>
                                    <p className="text-sm text-muted-foreground">
                                        Information about your organisation
                                    </p>
                                </div>

                                <div className="grid gap-4">
                                    <div className="grid gap-2">
                                        <Label htmlFor="organisation_name">
                                            Organisation name
                                        </Label>
                                        <Input
                                            id="organisation_name"
                                            type="text"
                                            name="organisation_name"
                                            required
                                            autoFocus
                                            tabIndex={1}
                                            placeholder="Acme Inc"
                                            value={organisationName}
                                            onChange={(e) =>
                                                setOrganisationName(
                                                    e.target.value,
                                                )
                                            }
                                        />
                                        <InputError
                                            message={errors.organisation_name}
                                        />
                                    </div>

                                    <div className="grid gap-2">
                                        <Label htmlFor="organisation_slug">
                                            Organisation domain
                                        </Label>
                                        <div className="flex items-center gap-2">
                                            <Input
                                                id="organisation_slug"
                                                type="text"
                                                name="organisation_slug"
                                                required
                                                tabIndex={2}
                                                placeholder="acme"
                                                value={slug}
                                                onChange={(e) => {
                                                    setSlug(e.target.value);
                                                    setSlugTouched(true);
                                                }}
                                                className="flex-1"
                                            />
                                            <span className="text-sm text-muted-foreground whitespace-nowrap">
                                                .{mainDomain}
                                            </span>
                                        </div>
                                        <InputError
                                            message={errors.organisation_slug}
                                        />
                                        {slug && !errors.organisation_slug && (
                                            <p className="text-sm text-muted-foreground">
                                                Your organisation will be
                                                accessible at{' '}
                                                <span className="font-medium">
                                                    {slug}.{mainDomain}
                                                </span>
                                            </p>
                                        )}
                                    </div>

                                    <div className="grid gap-2">
                                        <Label htmlFor="organisation_email">
                                            Organisation email (optional)
                                        </Label>
                                        <Input
                                            id="organisation_email"
                                            type="email"
                                            name="organisation_email"
                                            tabIndex={3}
                                            placeholder="contact@acme.com"
                                        />
                                        <InputError
                                            message={errors.organisation_email}
                                        />
                                    </div>
                                </div>
                            </div>

                            <Separator />

                            {/* Owner Details Section */}
                            <div className="space-y-4">
                                <div className="space-y-1">
                                    <h2 className="text-sm font-medium">
                                        Owner account
                                    </h2>
                                    <p className="text-sm text-muted-foreground">
                                        Your personal account details
                                    </p>
                                </div>

                                <div className="grid gap-4">
                                    <div className="grid gap-2">
                                        <Label htmlFor="owner_first_name">
                                            First name
                                        </Label>
                                        <Input
                                            id="owner_first_name"
                                            type="text"
                                            name="owner_first_name"
                                            required
                                            tabIndex={4}
                                            placeholder="John"
                                        />
                                        <InputError
                                            message={errors.owner_first_name}
                                        />
                                    </div>

                                    <div className="grid gap-2">
                                        <Label htmlFor="owner_last_name">
                                            Last name
                                        </Label>
                                        <Input
                                            id="owner_last_name"
                                            type="text"
                                            name="owner_last_name"
                                            required
                                            tabIndex={5}
                                            placeholder="Doe"
                                        />
                                        <InputError
                                            message={errors.owner_last_name}
                                        />
                                    </div>

                                    <div className="grid gap-2">
                                        <Label htmlFor="owner_email">
                                            Email address
                                        </Label>
                                        <Input
                                            id="owner_email"
                                            type="email"
                                            name="owner_email"
                                            required
                                            tabIndex={6}
                                            autoComplete="email"
                                            placeholder="john@acme.com"
                                        />
                                        <InputError
                                            message={errors.owner_email}
                                        />
                                    </div>

                                    <div className="grid gap-2">
                                        <Label htmlFor="owner_phone">
                                            Phone number (optional)
                                        </Label>
                                        <Input
                                            id="owner_phone"
                                            type="tel"
                                            name="owner_phone"
                                            tabIndex={7}
                                            placeholder="+1 (555) 000-0000"
                                        />
                                        <InputError
                                            message={errors.owner_phone}
                                        />
                                    </div>

                                    <div className="grid gap-2">
                                        <Label htmlFor="password">Password</Label>
                                        <Input
                                            id="password"
                                            type="password"
                                            name="password"
                                            required
                                            tabIndex={8}
                                            autoComplete="new-password"
                                            placeholder="Password"
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
                                            tabIndex={9}
                                            autoComplete="new-password"
                                            placeholder="Confirm password"
                                        />
                                        <InputError
                                            message={
                                                errors.password_confirmation
                                            }
                                        />
                                    </div>
                                </div>
                            </div>

                            <Button
                                type="submit"
                                className="mt-2 w-full"
                                tabIndex={10}
                                disabled={processing}
                                data-test="create-organisation-button"
                            >
                                {processing && <Spinner />}
                                Create organisation
                            </Button>
                        </div>

                        <div className="text-center text-sm text-muted-foreground">
                            Already have an account?{' '}
                            <TextLink href={login.url.toString()} tabIndex={11}>
                                Sign in
                            </TextLink>
                        </div>
                    </>
                )}
            </Form>
        </AuthLayout>
    );
}
