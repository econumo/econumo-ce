FROM node:lts-alpine as dev

WORKDIR /build/apps/app-frontend

RUN npm install -g @quasar/cli
COPY . /build
RUN npm install
RUN npm run build

FROM nginx:stable-alpine as prod

COPY --from=dev /build/apps/app-frontend/dist/spa /usr/share/nginx/html
COPY --from=dev /build/apps/app-frontend/build/docker/nginx/entrypoint.sh /entrypoint.sh
RUN rm -rf /build

CMD ["/entrypoint.sh"]
