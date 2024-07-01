FROM node:20-alpine

RUN apk update

RUN npm install -g serverless
RUN npm install -g serverless-offline
    # npm install -g yarn

RUN npm install -g nodemailer

WORKDIR /usr/src/app

# COPY package*.json ./

# RUN yarn

# COPY . .

ENV NODE_TLS_REJECT_UNAUTHORIZED=0

EXPOSE 3000

CMD [ "sls", "offline", "--host", "0.0.0.0" ]
