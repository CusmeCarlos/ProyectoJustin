import { ComponentFixture, TestBed } from '@angular/core/testing';
import { AdminUniversidadesPage } from './admin-universidades.page';

describe('AdminUniversidadesPage', () => {
  let component: AdminUniversidadesPage;
  let fixture: ComponentFixture<AdminUniversidadesPage>;

  beforeEach(() => {
    fixture = TestBed.createComponent(AdminUniversidadesPage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
