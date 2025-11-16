import { ComponentFixture, TestBed } from '@angular/core/testing';
import { AdminPreguntasPage } from './admin-preguntas.page';

describe('AdminPreguntasPage', () => {
  let component: AdminPreguntasPage;
  let fixture: ComponentFixture<AdminPreguntasPage>;

  beforeEach(() => {
    fixture = TestBed.createComponent(AdminPreguntasPage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
