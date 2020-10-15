import React from "react";
import {Link} from "react-router-dom";
import {makeStyles} from "@material-ui/core/styles";
import Particles from "react-particles-js";
import Container from "@material-ui/core/Container";
import CssBaseline from "@material-ui/core/CssBaseline";
import Box from "@material-ui/core/Box";

import Footer from "../layout/Footer";
import Grid from "@material-ui/core/Grid";
import Paper from "@material-ui/core/Paper";

const useStyles = makeStyles(theme => ({

    privacyBody: {
        padding: 20
    },
    privacyLogo: {
        textAlign: 'center',
        marginBottom: 50,
    }


}));


function Privacy(props) {
    const classes = useStyles();
    const today = new Date().getFullYear();

    return (
        <>
            <div className={'paperContainerPrivacy'}>
                <Container component="main">

                    <Grid container>
                        <Grid item xs={12}>
                            <Paper className={classes.privacyBody}>
                                <div className={classes.privacyLogo}>
                                    <Link to="/">
                                        <img
                                            alt="Logo"
                                            src="/media/agentadmin/logo/aa_full.png"
                                        />
                                    </Link>
                                </div>
                                <div>
                                    <h2>Privacy and Cookies Policy</h2>
                                    <p>
                                        <strong>United Agent Services, LLC (“UAS,” “we,” “us,” “our,” or the “Company”)
                                            is committed
                                            to
                                            protecting the privacy of anyone who visits our websites or registers to use
                                            our
                                            services or
                                            to attend our events.</strong>
                                    </p>
                                    <h4>Your Consent</h4>
                                    <p>Use of this site constitutes your consent to the terms of this Notice and the
                                        application of
                                        any applicable laws and regulations. Whenever you submit personal data and
                                        information via
                                        this site, you consent to the collection, use, and disclosure of that data and
                                        information
                                        in accordance with this Notice.</p>
                                    <h4>Collection of Information</h4>
                                    <p>Like many web sites, this site actively collects information from its visitors
                                        both by asking
                                        you specific questions when you interact with us (such as registering on our
                                        websites,
                                        signing up to receive information from us, or making a purchase) and by
                                        permitting you to
                                        communicate directly with us via e-mail. Some of the information that you submit
                                        may be
                                        personal data or information, that is: information relating to an identified or
                                        identifiable
                                        natural person. Information collected may include your name, username, mailing
                                        address,
                                        email address or other information which you provide to us so that we may
                                        communicate with
                                        you.
                                        Our websites will gather information such as what kind of browser you are on,
                                        what operating
                                        system you are using, your IP address, cookie information, time stamp (time page
                                        accessed as
                                        well as time spent per web page) and your clickstream information (for example,
                                        which pages
                                        you have viewed and how long you have been there). Note that information is only
                                        gathered
                                        while you are on the website.
                                        In compliance with the United States Children's Online Privacy Protection Act
                                        (COPPA), we do
                                        not seek information from children 13 and under.
                                        Use of Personal Information</p>
                                    <p>We will use and process your personal data lawfully, fairly and in a transparent
                                        manner. We
                                        will collect and use your personal data solely for the specified and legitimate
                                        purposes
                                        stated in this Notice. Except as otherwise stated, we may use your information
                                        to improve
                                        the content of our site, to customize the site to your preferences, to
                                        communicate
                                        information to you (if you have requested it), for our marketing and research
                                        purposes, for
                                        the fulfillment of services and related activities, and account management. </p>
                                    <p>We use your personal information to:
                                        <ul>
                                            <li>Set up your account and verify your identity</li>
                                            <li>Provide products or services and related activities you requested</li>
                                            <li>Provide direct and digital marketing communications via email, direct
                                                mail and
                                                telephone communication or through personalized online experiences
                                            </li>
                                            <li>Provide better customer service when you contact us</li>
                                            <li>Improve the quality, content and overall user experience of this site
                                            </li>
                                            <li>Administer surveys, contests and other promotional events</li>
                                            <li>Address other business needs such as website administration, security
                                                and fraud
                                                prevention, legal compliance and business continuity
                                            </li>
                                        </ul>
                                    </p>
                                    <h4>Sharing of Information with Third Parties</h4>
                                    <p>We understand that you do not want us to sell, rent or lease your personal
                                        information to
                                        third
                                        parties without your consent and we only share your personal information with
                                        third parties
                                        in
                                        certain situations. Examples of situations where we may share your personal
                                        information with
                                        third parties include:</p>
                                    <ul>
                                        <li>Sharing personal information with our service providers who provide services
                                            on our
                                            behalf on
                                            the condition that they are permitted to use that information solely in
                                            accordance with
                                            our
                                            instructions to provide requested services
                                        </li>
                                        <li>Sharing personal information with our business partners, but only to the
                                            extent you have
                                            purchased or expressed an interest in a product or service of such business
                                            partner or
                                            interacted with or otherwise authorized the sharing of your personal
                                            information with
                                            such
                                            business partner
                                        </li>
                                        <li>Sharing personal information with a third-party service provider to conduct
                                            our
                                            contests,
                                            surveys, or promotions, but only for the limited purpose of administering
                                            such contests,
                                            surveys
                                            or promotions
                                        </li>
                                        <li>Transferring personal information to a purchaser or successor entity in the
                                            event of a
                                            sale or
                                            other corporate transaction involving some or all of our business or as
                                            needed to affect
                                            the
                                            sale or transfer of business assets, or as needed for external audit,
                                            compliance or
                                            corporate
                                            governance related matters
                                        </li>
                                        <li>Disclosing information (i) as required by applicable law, court orders,
                                            government
                                            regulators
                                            or legal process (including subpoenas); (ii) as necessary to protect the
                                            rights or
                                            property of
                                            United Agent Services, LLC or this site; or (iii) in exigent circumstances
                                            to protect
                                            the
                                            personal safety of users of the site or other members of the public
                                            We also share your personal information with our affiliates for the purposes
                                            set forth
                                            in this
                                            Notice.
                                        </li>
                                    </ul>


                                    <h5>Use of Cookies</h5>
                                    <p> Our websites use "cookies". A cookie is a small text file that is stored on your
                                        computer,
                                        tablet, or smartphone when you visit a website that helps the site remember
                                        information
                                        about
                                        you and your preferences.
                                        You can find more information about the type of cookies we use and the purposes
                                        for which we
                                        use
                                        them below:</p>
                                    <ul>
                                        <li>How we use Cookies on our Websites and what Information we collect.</li>
                                    </ul>
                                    <h5>Session Cookies</h5>
                                    <p>We use session cookies for the following purposes:</p>
                                    <ul>
                                        <li>To control access to paid for subscription content</li>
                                        <li>To allow you to carry information across pages of our website and avoid
                                            having to
                                            re-enter
                                        </li>
                                    </ul>
                                    <h5>information</h5>
                                    <ul>
                                        <li>To allow you to access stored information for future use, such as
                                            registration
                                        </li>
                                        <li>To compile anonymous, aggregated statistics that allow us to understand how
                                            users use
                                            our
                                            website and to help us improve the structure of our website
                                        </li>
                                    </ul>
                                    <h5>Persistent Cookies</h5>
                                    <p>We use persistent cookies for the following purposes:</p>
                                    <ul>
                                        <li>To control access to paid for subscription content</li>
                                        <li>To help us recognize you as a unique visitor (just a number) when you return
                                            to our
                                            website
                                            and to allow us to tailor content to match your preferred interests
                                        </li>
                                        <li>To compile anonymous, aggregated statistics that allow us to understand how
                                            users use
                                            our
                                            website and to help us improve the structure of our website
                                        </li>
                                        <li>To internally identify you by account name, email address, customer ID, and
                                            location
                                            (geographic and computer ID/IP address)
                                        </li>
                                        <li>To tailor content to your preferences</li>

                                    </ul>
                                    <h5>Third Party Cookies</h5>
                                    <p>Third parties serve cookies via our websites. These are used for the following
                                        purposes:</p>
                                    <ul>
                                        <li>To serve content on websites and track whether this content is clicked on by
                                            users
                                        </li>
                                        <li>To control how often you are shown particular content</li>
                                        <li>To count the number of anonymous users of our websites</li>
                                        <li>For website usage analysis</li>
                                        <li>To link the information we store in cookies to any personally identifiable
                                            information
                                            you
                                            submit while on our websites
                                        </li>
                                    </ul>
                                    <h4>Links to Other Websites</h4>
                                    <p>This site may contain links or references to other web sites. Please be aware
                                        that we do not
                                        control other web sites and, except as otherwise noted in the applicable
                                        website, this
                                        Notice
                                        does not apply to those websites and we are not responsible for the security or
                                        privacy of
                                        any
                                        information collected by these third parties.</p>

                                    <h4>Share; Social Media Features; Email a Friend Functions; Public Forums</h4>
                                    <p>We offers certain “share”, “social media” functionality on some of our websites.
                                        If you
                                        choose
                                        to use these functions, we may and/or the third party social media network may
                                        collect
                                        certain
                                        information about you depending on the function or feature you use. We use this
                                        information
                                        for
                                        the sole purpose of sending this one-time email and we do not retain the
                                        information. With
                                        respect to the social media features (such as allowing you to post information
                                        about your
                                        activities or share your information with others on a social media site), the
                                        collection and
                                        use
                                        of information by and your interactions with the third party social media
                                        network will be
                                        governed by the privacy policy of the company providing those social media
                                        features. We are
                                        not
                                        responsible for the security or privacy of any information collected by these
                                        third
                                        parties.</p>

                                    <h4>Storage in Marketing and Customer Relationship Software Applications</h4>
                                    <p>Regardless of your consent to be contacted for direct marketing purposes, your
                                        personally
                                        identifiable data will be held in our marketing automation software and customer
                                        relationship
                                        management software for the purposes outlined above, and to properly track
                                        compliance, such
                                        as
                                        previous consent or opt-ins, as well as your company’s relationship history with
                                        us.</p>

                                    <h4>Security</h4>
                                    <p>We have implemented reasonable technical and organizational measures that are
                                        designed to
                                        prevent unauthorized access, unlawful processing and unauthorized or accidental
                                        loss,
                                        destruction or damage to your personal data on its servers. </p>

                                    <h4>Access and Correction; Your Rights</h4>
                                    <p> In some jurisdictions, you are entitled to request access to and correction of
                                        your personal
                                        information and, to the extent available under applicable law, you are also
                                        entitled to
                                        request
                                        the deletion of personal data and to restrict or object to processing. Any
                                        requests for
                                        access
                                        to or correction or deletion of your personal data, should be directed to the
                                        contact person
                                        listed under "Contact Information" below. Note that we may ask additional
                                        information from
                                        you,
                                        to confirm your identity, as we take the protection of your personal data
                                        seriously. In some
                                        cases, if you have an online account with us, you may be able to log into your
                                        account at
                                        any
                                        time to access and update the information you have provided to us.</p>
                                    <p> Furthermore, you have a right of access to the personal data that you have
                                        provided in a
                                        structured, commonly used and machine-readable format (portability), subject to
                                        a fee
                                        (except
                                        where it is not permissible under applicable law).</p>
                                    <p>You have the ability and the right to limit the information you share with us and
                                        the
                                        communications we send to you. When you opt-in to receive communications from
                                        us, you are
                                        giving
                                        your consent for your information to be used for us to contact you by email,
                                        direct mail and
                                        telephone, and to deliver personalized website experiences, to share information
                                        about
                                        relevant
                                        products, services, best practice information, industry news and events. You can
                                        amend your
                                        communication preferences, or opt-out completely.</p>


                                    <h4>International Data Transfers</h4>
                                    <p>We store and process your personal data in various countries worldwide including
                                        the United
                                        States. We may transfer and access such information from around the world,
                                        including the
                                        United
                                        States and other countries in which we have operations or in which our hosted
                                        service
                                        providers’
                                        cloud servers are located. By providing us with your personal data, you consent
                                        to this
                                        transfer. Your data shall be protected in accordance with this Notice regardless
                                        of where it
                                        is
                                        processed or stored.</p>

                                    <p>The following applies to data transfers outside the European Economic Area
                                        (“EEA): While
                                        using
                                        or otherwise processing your personal data for the purposes set out in this
                                        Notice, we may
                                        transfer your personal data to countries outside of the EEA. Whenever such
                                        transfer occurs,
                                        it
                                        will be based on the Standard Contractual Clauses (according to EU Commission
                                        Decision
                                        87/2010/EC or any future replacement) signed by the recipient of such personal
                                        data in
                                        accordance with applicable law.</p>

                                    <h4>Changes to This Privacy Policy</h4>
                                    <p>We may revise this Notice. You agree to be bound by any such revisions and should
                                        therefore
                                        periodically visit this page to determine the current terms to which you are
                                        bound. This
                                        Notice
                                        has been amended for the last time on May 11th 2020.</p>

                                    <h4>Contact Information</h4>
                                    <p>If you have any questions, comments or concerns about this Policy, you may
                                        contact us at by
                                        phone at (302) 467-3700 between 9am and 4pm ET or via email to
                                        privacy@unitedagentservices.com.</p>
                                    <h4>Disclosures</h4>
                                    <p>If we are required by law or legal process, we will disclose or provide access to
                                        your PII or
                                        Business Information to such government or judicial authority as specified by
                                        such law or
                                        legal
                                        process.</p>
                                    <h4>Options</h4>
                                    <p>Upon request we will provide customers with access to their own records.
                                        Customers can access
                                        and correct their PII or Business Information by emailing us Monday – Friday 9am
                                        to 4pm ET
                                        at
                                        privacy@unitedagentservices.com, using the username and phone number provided
                                        during
                                        ordering.</p>

                                    <h4>California Privacy Rights</h4>
                                    <p>California Civil Code Section 1798.83 permits customers of United Agent Services,
                                        LLC who are
                                        California residents to request certain information regarding the disclosure of
                                        any personal
                                        information, including PII and Business Information, to third parties for their
                                        direct
                                        marketing
                                        purposes. To make such a request, please send an email at
                                        privacy@unitedagentservices.com</p>

                                    <h4>Additional Information for California Residents</h4>
                                    <p>In addition to the information provided in this privacy policy, additional
                                        information for
                                        California residents can be found below.</p>

                                    <p>Pursuant to California law, we are providing additional information to California
                                        residents.
                                        Under California law, certain organizations need to disclose whether the
                                        following
                                        categories of
                                        “personal information” are collected or disclosed for an organization’s
                                        “business purpose”
                                        as
                                        those terms are defined under California law. Below please find the categories
                                        of personal
                                        information about California residents that we collect or disclose to third
                                        parties or
                                        service
                                        providers. Note that while a category may be marked that does not necessarily
                                        mean that we
                                        have
                                        information in that category about you. For example, while we collect credit
                                        card numbers
                                        for
                                        customers who purchase our products, we do not collect or transfer credit card
                                        numbers of
                                        individuals that submit questions on our website’s “contact us” page. We do not
                                        sell
                                        personal
                                        information.</p>

                                    <h4>Personal Information</h4>
                                    <p>Information is Collected By Us and Disclosed for Business Purposes</p>
                                    <ul>
                                        <li>Audio, electronic, visual, thermal, olfactory, physical characteristics or
                                            similar
                                        </li>
                                        <li>Credit or debit card number, bank account number or other financial
                                            information
                                        </li>
                                        <li>Commercial or transactional information (e.g., products or services
                                            purchased, or
                                            other purchasing or consuming histories or tendencies)
                                        </li>

                                        <li>Electronic network activity (e.g., browsing history)</li>
                                        <li>Contact information (e.g., name or alias, postal address, email address,
                                            telephone
                                            number,
                                            signature)
                                        </li>
                                        <li>Online identifier (e.g. IP address)</li>
                                    </ul>
                                    <h4>Information is Collected By Us but Not Disclosed for Business Purposes</h4>
                                    <ul>
                                        <li> Social Security Number, Driver’s License Number, State ID, Passport Number,
                                            Insurance Policy
                                        </li>
                                    </ul>
                                    <h5>Number or Signature</h5>
                                    <p>Information is Not Collected By Us and Information is Not Disclosed for Business
                                        Purposes</p>
                                    <ul>
                                        <li>Biometric information</li>
                                        <li>Characteristics of protected classifications (e.g., age, sex, race,
                                            ethnicity,
                                            physical or
                                            mental handicap, etc.)
                                        </li>
                                        <li>Professional, employment, employment history or education information
                                            • Geolocation data
                                        </li>
                                        <li>Medical or health information not covered by HIPAA or health insurance
                                            information
                                        </li>
                                    </ul>
                                    <p><strong>We and our third-party service providers may collect the above categories
                                        of
                                        personal
                                        information from the following sources:</strong></p>
                                    <ul>
                                        <li>Direct Interactions, such as, when you register for our services or make a
                                            purchase
                                        </li>
                                        <li>Data from Third Parties, such as, information on third-party websites or
                                            other
                                            information you
                                            may have made publicly available or information provided by third-party
                                            sources,
                                            including but
                                            not limited to government entities and data resellers
                                        </li>
                                        <li> Automated Tracking Technologies, such as, information automatically
                                            collected
                                            about
                                            your
                                            interaction with our services and websites using various technologies such
                                            as
                                            cookies,
                                            web logs
                                            and beacons and internet tags.
                                        </li>
                                    </ul>
                                    <p>Depending on how you interact with us and our services, we may use and disclose
                                        the
                                        above
                                        categories of personal information for the following purposes:</p>
                                    <ul>
                                        <li>Administering our relationship with you and our business, such as, providing
                                            and
                                            managing your
                                            access to and use of our services.
                                        </li>
                                        <li>Improving our Services, such as inviting you to participate in surveys or to
                                            personalize your
                                            experience with our services.
                                        </li>
                                        <li>Marketing, such as registering your opt-in to receive marketing
                                            communications
                                            when
                                            applicable.
                                        </li>
                                        <li>Other general business support purposes, including but not limited to,
                                            procurement,
                                            financial
                                            and fiscal management, risk and compliance management, and external
                                            reporting.
                                        </li>
                                    </ul>
                                    <p><strong>We may share the above categories of personal information to the
                                        following third
                                        parties:</strong></p>
                                    <ul>
                                        <li>other United Agent Services affiliates or subsidiaries, e.g. for providing
                                            underpinning
                                            technology to support the services we deliver;
                                        </li>
                                        <li>our service providers, e.g. for managing or hosting services and/or
                                            underpinning
                                            technology
                                            for the services we provide;
                                        </li>
                                        <li>our business partners, to the extent you have purchased or expressed
                                            interest in, a
                                            product or
                                            service of such business partner, interacted with them or otherwise
                                            authorized the
                                            sharing of
                                            your personal information with such business partner;
                                        </li>
                                        <li>subscribing, accrediting or professional organizations, e.g. for providing
                                            utilization
                                            information to organizations that provide you with access to our services
                                            and/or sharing
                                            tracking and redeeming credits for professional accreditation;
                                        </li>
                                        <li>organizations involved in business transfers, e.g.to a purchaser or
                                            successor entity
                                            in the
                                            event of a sale or any other corporate transaction involving some or all of
                                            our
                                            business;
                                        </li>
                                        <li>other parties, e.g. as needed for external audit, compliance, risk
                                            management,
                                            corporate
                                            development and/or corporate governance related matters; or
                                        </li>
                                        <li>governmental authorities and regulators, as required under applicable law.
                                        </li>
                                    </ul>
                                    <h4>Exercising Rights to Request Access and Request Deletion</h4>
                                    <p>Subject to certain exceptions, California residents have the right to request
                                        access,
                                        deletion
                                        and portability of their personal information as further described in the
                                        Privacy & Cookie
                                        Notice. If you would like to submit a request or have additional questions about
                                        the
                                        personal
                                        information that we have about you, please contact us by contacting us at
                                        privacy@unitedagentservices.com.</p>

                                    <p>When you submit your request, we will take steps to attempt to verify your
                                        identity. We will
                                        seek to match the information in your request to the personal information we
                                        maintain about
                                        you.
                                        As part of our verification process, we may ask you to submit additional
                                        information, use
                                        identity verification services to assist us, or if you have set up an account on
                                        our
                                        website, we
                                        may ask you to sign in to your account as part of our identity verification
                                        process. Please
                                        understand that, depending on the type of request you submit, to protect the
                                        privacy and
                                        security of your personal information, we will only complete your request when
                                        we are
                                        satisfied
                                        that we have verified your identity to a reasonable degree of certainty.</p>

                                    <p>We do not discriminate against individuals who exercise their rights under
                                        applicable law.
                                        If we receive a request from an authorized agent, we have the right to verify
                                        with the data
                                        subject that the data subject indeed wants to take the action requested by the
                                        agent and
                                        will do
                                        so by contacting the data subject directly.</p>


                                </div>
                            </Paper>
                        </Grid>
                    </Grid>

                    <Box>
                        <div className="privacy-policy-footer">
                            <Footer/>
                        </div>
                    </Box>
                </Container>

            </div>


        </>);
}

export default Privacy;


