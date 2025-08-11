FROM gcr.io/distroless/static-debian12

COPY kinhweb /kinhweb

EXPOSE 8080

CMD ["/kinhweb"]