const b64 = require('base64-js');
const encryptionSdk = require('@aws-crypto/client-node');

const { encrypt, decrypt } = encryptionSdk.buildClient(encryptionSdk.CommitmentPolicy.REQUIRE_ENCRYPT_ALLOW_DECRYPT);

const generatorKeyId = process.env.KEY_ALIAS;
const keyIds = [ process.env.KEY_ARN ];
const keyring = new encryptionSdk.KmsKeyringNode({ generatorKeyId, keyIds });

const { createTransport } = require('nodemailer');

module.exports.handler = async (event, context, callback) => {

  const from = 'kotana@local';
  const to = event.request.userAttributes.email;

  //Decrypt the secret code using encryption SDK.
  let plainTextCode;
  if(event.request.code){
    const { plaintext, messageHeader } = await decrypt(keyring, b64.toByteArray(event.request.code));
    plainTextCode = plaintext;
  }

  //PlainTextCode now contains the decrypted secret.
  if(event.triggerSource == 'CustomEmailSender_SignUp'){
    const text = `Welcome to Kotana!

Complete your registration with this temporary code: ${plainTextCode}.

This code is valid for 7 days, after that, your registration request will be removed.

Cheers
`;
    const email = await emailSender(from, to, 'Code', text);
  }
  else if(event.triggerSource == 'CustomEmailSender_ResendCode'){
  }
  else if(event.triggerSource == 'CustomEmailSender_ForgotPassword'){
  }
  else if(event.triggerSource == 'CustomEmailSender_UpdateUserAttribute'){
  }
  else if(event.triggerSource == 'CustomEmailSender_VerifyUserAttribute'){
  }
  else if(event.triggerSource == 'CustomEmailSender_AdminCreateUser'){
  }
  else if(event.triggerSource == 'CustomEmailSender_AccountTakeOverNotification'){
  }
  return callback(null, event);
};


async function emailSender(from, to, subject, text) {

  const transporter = createTransport(process.env.SMTP_URL);

  const mailOptions = {
    from: from,
    to: to,
    subject: subject,
    text: text
  };

  transporter.sendMail(mailOptions, function(error, info){
    if (error) {
      console.log(error);
    } else {
      console.log('Email sent: ' + info.response);
    }
  });

}
