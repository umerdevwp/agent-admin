import Typography from "@material-ui/core/Typography";
import Link from "@material-ui/core/Link";
import React from "react";

export default function Footer() {


    return (
        <Typography variant="body2" color="textSecondary" align="center">
            {'Copyright © '}
            <Link color="inherit" href="/">
                AgentAdmin
            </Link>{' '}
            {new Date().getFullYear()}
            {'. '}
            <Link color="inherit" href="/privacy-policy">
                Privacy Policy
            </Link>
        </Typography>
    );
}
