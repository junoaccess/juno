import { SVGAttributes } from 'react';

export default function AppLogoIcon(props: SVGAttributes<SVGElement>) {
    return (
        <img
            src="/logo.svg"
            alt="Juno"
            className={props.className}
            style={{ width: '100%', height: '100%' }}
        />
    );
}
