FROM node:14

ENV INSTANA_AUTO_PROFILE true

EXPOSE 8080

WORKDIR /opt/server

COPY package.json /opt/server/

RUN npm install

COPY *.js /opt/server/

CMD ["/bin/sh", "-c", "node --require './tracing.js' server.js"]

