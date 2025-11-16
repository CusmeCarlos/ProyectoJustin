import { ComponentFixture, TestBed } from '@angular/core/testing';
import { AdminHistorialPage } from './admin-historial.page';

describe('AdminHistorialPage', () => {
  let component: AdminHistorialPage;
  let fixture: ComponentFixture<AdminHistorialPage>;

  beforeEach(() => {
    fixture = TestBed.createComponent(AdminHistorialPage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
