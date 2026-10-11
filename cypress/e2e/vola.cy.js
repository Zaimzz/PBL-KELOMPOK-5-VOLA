
describe('Aplikasi PBL Vola', () => {
  it('menampilkan halaman Vola', () => {
    cy.visit('http://127.0.0.1:8000');
    cy.get('body').should('be.visible');
  });
});