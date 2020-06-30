<style id="startup">
  @media screen and (max-width: 474px) {
    header.main .intro   { height: calc(30vh + 32px); }
    main:not(.no-header) { min-height: calc(70vh - 32px); }
  }
  @media screen and (min-width: 475px) {
    header.main .intro   { height: calc(30vh + 64px); }
    main:not(.no-header) { min-height: calc(70vh - 64px); }
  }
  body {
    background-color: #0f1d2c;
  }
  > * {
    opacity: 0;
  }
  main.no-header {
    min-height: 100%;
  }
</style>
