<style id="startup">
  @media screen and (max-width: 474px) {
    header.main .intro   { height: 30vh; }
    main:not(.no-header) { min-height: 70vh; }
  }
  @media screen and (min-width: 475px) {
    header.main .intro   { height: calc(30vh + 64px); }
    main:not(.no-header) { min-height: calc(70vh- 64px); }
  }
  body {
    background-color: #0f1e2d;
  }
  body * {
    opacity: 0;
  }
  main.no-header {
    min-height: 100%;
  }
</style>
