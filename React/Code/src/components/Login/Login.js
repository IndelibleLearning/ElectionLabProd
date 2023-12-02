import React, { useEffect, useState } from 'react';
import * as request from 'common/request.js';
import * as user_common from 'common/user_common.js';
import './Login.scss';
import loginTitle from "assets/LabTitle2.svg";
import { useNavigate } from 'react-router-dom';

const Login = () => {
    let navigate = useNavigate();
    const [isLoading, setIsLoading] = useState(false);

    useEffect(() => {
        window.google.accounts.id.initialize({
            client_id: '887227153285-7ol0k70995346nef307c9ckesrai7rft.apps.googleusercontent.com',
            callback: handleCredentialResponse
        });
    }, []);

    const handleCredentialResponse = (response) => {
        console.log(response);
        // Process the Google ID token
        sendTokenToServer(response.credential);
    };

    const sendTokenToServer = (token) => {
        let url = user_common.LOGIN_GOOGLE_URL;
        let postData = {
            "google_id": token
        };

        request.post(url, postData)
            .then(res => {
                console.log(res);
                if (res.has_errors)
                {
                    alert(res.err_msg);
                    setIsLoading(false); // Start loading
                    return;
                }

                // successful login
                user_common.setLoginToken(res.data.login_token);
                user_common.setUserName(res.data.username);

                // redirect to
                navigate('/dashboard');
            });
    };

    const onGoogleSignIn = () => {
        setIsLoading(true); // Start loading
        window.google.accounts.id.prompt();
    };

    return (
        <div className="login-main">
            <div className="login-container">
                <img className="login-title" src={loginTitle}/>
                <div className="login-form">
                    <button
                        id="google-submit"
                        onClick={onGoogleSignIn}
                        disabled={isLoading}
                    >
                        {isLoading ? 'Logging in...' : 'Login with Google'}
                    </button>
                </div>
            </div>
        </div>
    );
};

export default Login;
