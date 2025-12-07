import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AuthLayout from '@/layouts/auth-layout';
import { Form, Head } from '@inertiajs/react';

interface OrganizationSelectProps {
    mainDomain: string;
}

export default function OrganizationSelect({
    mainDomain,
}: OrganizationSelectProps) {
    return (
        <AuthLayout
            title="Sign in to your organisation"
            description="Enter your organisation domain to continue"
        >
            <Head title="Select Organisation" />

            <Form action="/login" method="post" className="flex flex-col gap-6">
                {({ processing, errors }) => (
                    <>
                        <div className="grid gap-6">
                            <div className="grid gap-2">
                                <Label htmlFor="slug">
                                    Organisation domain
                                </Label>
                                <div className="flex items-center gap-2">
                                    <Input
                                        id="slug"
                                        type="text"
                                        name="slug"
                                        required
                                        autoFocus
                                        autoComplete="off"
                                        placeholder="acme"
                                        className="flex-1"
                                    />
                                    <span className="text-sm text-muted-foreground">
                                        .{mainDomain}
                                    </span>
                                </div>
                                <InputError message={errors.slug} />
                            </div>

                            <Button
                                type="submit"
                                className="mt-4 w-full"
                                disabled={processing}
                            >
                                {processing && <Spinner />}
                                Continue
                            </Button>
                        </div>
                    </>
                )}
            </Form>
        </AuthLayout>
    );
}
