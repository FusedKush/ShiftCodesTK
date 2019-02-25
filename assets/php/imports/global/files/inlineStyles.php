<style id="startup">
  @media screen and (max-width: 474px) {
    header.main          { height: calc(30% + 32px); }
    main:not(.no-header) { min-height: calc(70% - 32px) }
  }
  @media screen and (min-width: 475px) {
    header.main          { height: calc(30% + 64px); }
    main:not(.no-header) { min-height: calc(70% - 64px) }
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
